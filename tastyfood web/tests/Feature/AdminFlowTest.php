<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\News;
use App\Models\Gallery;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminFlowTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_unauthenticated_admin_redirects_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_works(): void
    {
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin, 'admin');
    }

    public function test_berita_crud_flow(): void
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->post(route('berita.store'), [
            'title' => 'Test News',
            'content' => 'Test content',
            'author' => 'Test Author',
            'status' => 'published',
        ]);
        $response->assertRedirect(route('berita.index'));
        $this->assertDatabaseHas('news', ['title' => 'Test News']);

        $news = News::where('title', 'Test News')->first();
        $response = $this->put(route('berita.update', $news), [
            'title' => 'Updated News',
            'content' => 'Updated content',
            'author' => 'Test Author',
            'status' => 'draft',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('berita.index'));
        $this->assertDatabaseHas('news', ['id' => $news->id, 'title' => 'Updated News']);

        $response = $this->delete(route('berita.destroy', $news));
        $response->assertRedirect(route('berita.index'));
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    public function test_galeri_crud_flow(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin, 'admin');

        $image = UploadedFile::fake()->create('gallery.jpg', 100);
        $response = $this->post(route('galeri.store'), [
            'title' => 'Test Gallery',
            'image' => $image,
            'description' => 'Test description',
            'category' => 'food',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('galeri.index'));
        $this->assertDatabaseHas('galleries', ['title' => 'Test Gallery']);

        $gallery = Gallery::where('title', 'Test Gallery')->first();
        $response = $this->put(route('galeri.update', $gallery), [
            'title' => 'Updated Gallery',
            'description' => 'Updated description',
            'category' => 'food',
            'status' => 'inactive',
        ]);
        $response->assertRedirect(route('galeri.index'));
        $this->assertDatabaseHas('galleries', ['title' => 'Updated Gallery']);

        $response = $this->delete(route('galeri.destroy', $gallery));
        $response->assertRedirect(route('galeri.index'));
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }

    public function test_contact_form_creates_message_visible_in_admin(): void
    {
        $response = $this->post(route('contact.store'), [
            'subject' => 'Test Subject',
            'name' => 'Test User',
            'email' => 'user@test.com',
            'message' => 'Test message content',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('messages', ['subject' => 'Test Subject']);

        $this->actingAs($this->admin, 'admin');
        $message = Message::where('subject', 'Test Subject')->first();
        $response = $this->get(route('pesan.index'));
        $response->assertSee('Test Subject');
    }
}
