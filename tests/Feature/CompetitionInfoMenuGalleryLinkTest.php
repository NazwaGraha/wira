<?php

namespace Tests\Feature;

use App\Models\CompetitionInfoMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionInfoMenuGalleryLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_gallery_menu_points_to_correct_instagram(): void
    {
        $items = CompetitionInfoMenu::defaultItems();
        $galleryItem = collect($items)->firstWhere('category_badge', 'Galeri Media');

        $this->assertNotNull($galleryItem);
        $this->assertEquals('https://www.instagram.com/pmr_sman1c', $galleryItem['url_link']);
        $this->assertEquals('Buka Galeri Foto & Video', $galleryItem['button_text']);
    }

    public function test_model_accessor_returns_correct_instagram_link(): void
    {
        $menu = CompetitionInfoMenu::create([
            'title' => 'Dokumentasi Kegiatan',
            'category_badge' => 'Galeri Media',
            'icon' => 'fa-solid fa-photo-film',
            'color_theme' => 'cyan',
            'description' => 'Koleksi dokumentasi kegiatan lomba.',
            'action_type' => 'link',
            'url_link' => 'https://instagram.com/pmrwirasman1c', // old link
            'button_text' => 'Buka Galeri Foto & Video',
            'is_active' => true,
        ]);

        $this->assertEquals('https://www.instagram.com/pmr_sman1c', $menu->url_link);
        $this->assertEquals('link', $menu->action_type);
    }

    public function test_lomba_page_displays_instagram_link_for_gallery_media(): void
    {
        CompetitionInfoMenu::create([
            'title' => 'Dokumentasi Kegiatan',
            'category_badge' => 'Galeri Media',
            'icon' => 'fa-solid fa-photo-film',
            'color_theme' => 'cyan',
            'description' => 'Koleksi foto dan video rekaman lomba.',
            'action_type' => 'link',
            'url_link' => 'https://www.instagram.com/pmr_sman1c',
            'button_text' => 'Buka Galeri Foto & Video',
            'is_active' => true,
        ]);

        $response = $this->get(route('lomba.index'));

        $response->assertStatus(200);
        $response->assertSee('https://www.instagram.com/pmr_sman1c');
        $response->assertSee('Buka Galeri Foto & Video');
    }
}
