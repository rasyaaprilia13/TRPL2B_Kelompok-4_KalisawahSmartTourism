<?php

namespace App\Http\Controllers\API\Kelola_landingpage\hero_section;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingSettingsController extends Controller
{
    /**
     * Ambil landing setting
     */
    public function index()
    {
        $setting = LandingSetting::first();

        // return response()->json([
        //     'success' => true,
        //     'data' => $setting
        // ]);

        return view('admin.kelola_halaman.landing', compact('setting'));

    }

    /**
     * Tambah landing setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_image_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_image_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // cek apakah sudah ada data lama
        // Ambil data pertama
        $oldSetting = LandingSetting::first();

        // JIKA data lama ada, baru lakukan penghapusan
        if ($oldSetting) {
            if ($oldSetting->hero_image_path && Storage::disk('public')->exists($oldSetting->hero_image_path)) {
                Storage::disk('public')->delete($oldSetting->hero_image_path);
            }
            if ($oldSetting->hero_image_path_2 && Storage::disk('public')->exists($oldSetting->hero_image_path_2)) {
                Storage::disk('public')->delete($oldSetting->hero_image_path_2);
            }
            if ($oldSetting->hero_image_path_3 && Storage::disk('public')->exists($oldSetting->hero_image_path_3)) {
                Storage::disk('public')->delete($oldSetting->hero_image_path_3);
                }
            // hapus data lama dari database
            $oldSetting->delete();
        }

        // buat data baru
        $setting = new LandingSetting();

        /**
         * Hero title
         */
        if ($request->filled('hero_title')) {
            $setting->hero_title = $request->hero_title;
        }

        /**
         * Hero subtitle
         */
        if ($request->filled('hero_subtitle')) {
            $setting->hero_subtitle = $request->hero_subtitle;
        }

        /**
         * Hero image
         */
        if ($request->hasFile('hero_image')) {

            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('hero_image')->getClientOriginalExtension();

            // simpan file ke storage/app/landing
            $path = $request->file('hero_image')->storeAs('landing', $filename, 'public');

            // simpan path file
            $setting->hero_image_path = $path;
        }
        // Hero image 2
        if ($request->hasFile('hero_image_2')) {
            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('hero_image_2')->getClientOriginalExtension();

            // simpan file ke storage/app/landing
            $path = $request->file('hero_image_2')->storeAs('landing', $filename, 'public');

            // simpan path file
            $setting->hero_image_path_2 = $path;
        }
        //Hero image 3
        if ($request->hasFile('hero_image_3')) {
            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('hero_image_3')->getClientOriginalExtension();

            // simpan file ke storage/app/landing
            $path = $request->file('hero_image_3')->storeAs('landing', $filename, 'public');

            // simpan path file
            $setting->hero_image_path_3 = $path;
        }

        // simpan data
        $setting->save();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Landing setting berhasil ditambahkan',
        //     'data' => $setting
        // ], 201);

        return redirect()->route('admin.landing-settings.index')->with('success', 'Landing setting berhasil disimpan!');
    }

    /**
     * Update landing setting
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_image_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_image_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // ambil data berdasarkan id
        $setting = LandingSetting::findOrFail($id);

        /**
         * Update hero title
         */
        if ($request->filled('hero_title')) {
            $setting->hero_title = $request->hero_title;
        }

        /**
         * Update hero subtitle
         */
        if ($request->filled('hero_subtitle')) {
            $setting->hero_subtitle = $request->hero_subtitle;
        }

        /**
         * Update hero image
         */
        if ($request->hasFile('hero_image')) {
            if ($setting->hero_image_path && Storage::disk('public')->exists($setting->hero_image_path)) {
                Storage::disk('public')->delete($setting->hero_image_path);
            }

            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('hero_image')->getClientOriginalExtension();

            // simpan file baru
            $path = $request->file('hero_image')->storeAs('landing', $filename, 'public');

            // simpan path file baru
            $setting->hero_image_path = $path;
        }

        // Hero image 2
        if ($request->hasFile('hero_image_2')) {
            if ($setting->hero_image_path_2 && Storage::disk('public')->exists($setting->hero_image_path_2)) {
                Storage::disk('public')->delete($setting->hero_image_path_2);
            }

            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('hero_image_2')->getClientOriginalExtension();

            // simpan file baru
            $path = $request->file('hero_image_2')->storeAs('landing', $filename, 'public');

            // simpan path file baru
            $setting->hero_image_path_2 = $path;
        }

        // Hero image 3
        if ($request->hasFile('hero_image_3')) {
            if ($setting->hero_image_path_3 && Storage::disk('public')->exists($setting->hero_image_path_3)) {
                Storage::disk('public')->delete($setting->hero_image_path_3);
            }

            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('hero_image_3')->getClientOriginalExtension();

            // simpan file baru
            $path = $request->file('hero_image_3')->storeAs('landing', $filename, 'public');

            // simpan path file baru
            $setting->hero_image_path_3 = $path;
        }

        // simpan perubahan
        $setting->save();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Landing setting berhasil diperbarui',
        //     'data' => $setting
        // ]);

        return redirect()->route('admin.landing-settings.index')->with('success', 'Landing setting berhasil diperbarui!');
    }
}
