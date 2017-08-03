<?php

namespace Tests\Feature;

use App\Team;
use App\Ticket;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TeamTicketTest extends TestCase
{
    use DatabaseMigrations;

    private function validParams($overrides = []){
        return array_merge([
            "requester"     => "johndoe",
            "title"         => "App is not working",
            "body"          => "I can't log in into the application",
            "tags"          => ["xef"]
        ], $overrides);
    }

    /** @test */
    public function can_create_a_ticket(){

        $team = factory(Team::class)->create();

        $response = $this->post('api/tickets',[
            "requester"     => "johndoe",
            "title"         => "App is not working",
            "body"          => "I can't log in into the application",
            "tags"          => ["xef"],
            "team_id"       => $team->id
        ]);

        $response->assertStatus( Response::HTTP_CREATED );
        $response->assertJson(["data" => ["id" => 1]]);

        tap( Ticket::first(), function($ticket) use($team) {
            $this->assertEquals ( $ticket->requester, "johndoe");
            $this->assertEquals ( $ticket->title, "App is not working");
            $this->assertEquals ( $ticket->body, "I can't log in into the application");
            $this->assertTrue   ( $ticket->tags->pluck('name')->contains("xef") );
            $this->assertEquals( Ticket::STATUS_NEW, $ticket->status);
            $team->tickets->contains($ticket);
        });
    }


}