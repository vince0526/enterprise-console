<?php

namespace Tests\Feature;

use App\Models\CoreDatabase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmcCoreSubmodulesUITest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
        $this->seed(\Database\Seeders\CoreSubmodulesSeeder::class);
    }

    public function test_core_registry_page_loads_and_shows_registry_tab()
    {
        $response = $this->get(route('emc.core.index'));

        $response->assertStatus(200);
        $response->assertSee('Database Registry');
        $response->assertViewHas('activeTab', 'registry');
    }

    public function test_ownership_tab_is_accessible_and_displays_data()
    {
        $response = $this->get(route('emc.core.index', ['tab' => 'ownership']));

        $response->assertStatus(200);
        $response->assertSee('Ownership:');
        $response->assertViewHas('activeTab', 'ownership');
        $response->assertSee('Add New Owner');
    }

    public function test_can_add_new_owner()
    {
        $db = CoreDatabase::first();

        $response = $this->post(route('emc.core.owners.store'), [
            'core_database_id' => $db->id,
            'owner_name' => 'Test Owner',
            'role' => 'Test Role',
            'effective_date' => '2023-01-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('core_database_owners', [
            'core_database_id' => $db->id,
            'owner_name' => 'Test Owner',
        ]);
    }

    public function test_can_delete_owner()
    {
        $db = CoreDatabase::with('owners')->first();
        $owner = $db->owners->first();

        $response = $this->delete(route('emc.core.owners.destroy', $owner));

        $response->assertRedirect();
        $this->assertDatabaseMissing('core_database_owners', ['id' => $owner->id]);
    }

    public function test_lifecycle_tab_is_accessible_and_displays_data()
    {
        $response = $this->get(route('emc.core.index', ['tab' => 'lifecycle']));

        $response->assertStatus(200);
        $response->assertSee('Lifecycle Events:');
        $response->assertViewHas('activeTab', 'lifecycle');
        $response->assertSee('Add New Lifecycle Event');
    }

    public function test_can_add_new_lifecycle_event()
    {
        $db = CoreDatabase::first();

        $response = $this->post(route('emc.core.lifecycle-events.store'), [
            'core_database_id' => $db->id,
            'event_type' => 'Test Event',
            'details' => 'Some details.',
            'effective_date' => '2023-01-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('core_database_lifecycle_events', [
            'core_database_id' => $db->id,
            'event_type' => 'Test Event',
        ]);
    }

    public function test_can_delete_lifecycle_event()
    {
        $db = CoreDatabase::with('lifecycleEvents')->first();
        $event = $db->lifecycleEvents->first();

        $response = $this->delete(route('emc.core.lifecycle-events.destroy', $event));

        $response->assertRedirect();
        $this->assertDatabaseMissing('core_database_lifecycle_events', ['id' => $event->id]);
    }

    public function test_links_tab_is_accessible_and_displays_data()
    {
        $response = $this->get(route('emc.core.index', ['tab' => 'links']));

        $response->assertStatus(200);
        $response->assertSeeText('Linked Connections & Policies:', false);
        $response->assertViewHas('activeTab', 'links');
        $response->assertSee('Add New Link');
    }

    public function test_can_add_new_link()
    {
        $db = CoreDatabase::first();

        $response = $this->post(route('emc.core.links.store'), [
            'core_database_id' => $db->id,
            'linked_connection_name' => 'test-connection',
            'link_type' => 'Test Link',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('core_database_links', [
            'core_database_id' => $db->id,
            'linked_connection_name' => 'test-connection',
        ]);
    }

    public function test_can_delete_link()
    {
        $db = CoreDatabase::with('links')->first();
        $link = $db->links->first();

        $response = $this->delete(route('emc.core.links.destroy', $link));

        $response->assertRedirect();
        $this->assertDatabaseMissing('core_database_links', ['id' => $link->id]);
    }
}
