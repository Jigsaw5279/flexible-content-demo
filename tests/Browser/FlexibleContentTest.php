<?php

namespace Tests\Browser;

use App\Models\User;
use Database\Seeders\NovaUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Laravel\Nova\Testing\Browser\Pages\Create;
use Laravel\Nova\Testing\Browser\Pages\Update;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class FlexibleContentTest extends DuskTestCase
{
    use RefreshDatabase;

    protected string $seeder = NovaUserSeeder::class;

    #[Test]
    public function userCanBeCreated(): void
    {
        $this->browse(function (Browser $browser) {
            $page = new Create('users');
            $browser->loginAs(1)
                ->visit($page)
                ->type('@name', 'Some User')
                ->type('@email', 'some@email.com')
                ->type('@password', 'secret-password')
                ->click('@toggle-layouts-dropdown-or-add-default')
                ->click('@add-wysiwyg')
                ->type('#title-default-text-field', 'My Title')
                ->type('#content-default-textarea-field', 'Some content')
                ->screenshot($this->stepName('after Title'));

            $page->create($browser);

            $browser->screenshot($this->name());
            $browser->assertSee('The user was created!');

            $this->assertDatabaseHas(User::class, [
                'name'  => 'Some User',
                'email' => 'some@email.com',
            ]);
        });


    }

    #[Test]
    public function userCanBeEdited(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $page = new Update('users', 1);
            $browser->loginAs($user->id)
                ->visit($page)
                ->type('@name', 'Other User')
                ->type('@email', 'other@email.com');

            $page->updateAndContinueEditing($browser);

            $browser->screenshot($this->name());
            $browser->assertSee('The user was updated!');

            $this->assertDatabaseHas(User::class, [
                'id' => $user->id,
                'name'  => 'Other User',
                'email' => 'other@email.com',
            ]);
        });


    }
}
