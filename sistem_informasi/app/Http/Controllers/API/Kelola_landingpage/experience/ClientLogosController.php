<?php

namespace App\Http\Controllers\API\Kelola_landingpage\experience;

use App\Http\Controllers\Controller;
use App\Models\ClientLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientLogosController extends Controller
{
    // halo ini peruban saya
    /**
     * Ambil semua logo perusahaan
     */
    public function index()
    {
        $logos = ClientLogo::latest()->get();

        // return response()->json([
        //     'success' => true,
        //     'data' => $logos
        // ]);
        return view('admin.kelola_halaman.logo', compact('logos'));
    }

    /**
     * Tambah logo perusahaan
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'logo_image_path' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            // generate nama file aman
            $filename = Str::uuid() . '.' .
                $request->file('logo_image_path')->getClientOriginalExtension();

            // simpan file ke storage/app/logos
            $path = $request->file('logo_image_path')->storeAs('logos', $filename, 'public');

            // simpan database
            $logo = ClientLogo::create([
                'company_name' => $request->company_name,
                'logo_image_path' => $path,
            ]);

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Logo perusahaan berhasil ditambahkan',
            //     'data' => $logo
            // ], 201);

            return redirect()->back()->with('success', 'Logo perusahaan berhasil ditambahkan');


        } catch (\Exception $e) {

            // return response()->json([
            //     'message' => $e->getMessage()
            // ], 500);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan logo perusahaan');
        }
    }

    /**
     * Update logo perusahaan
     */
    public function update(Request $request, $id)
    {
        try {

            $validated = $request->validate([
                'company_name' => 'nullable|string|max:255',
                'logo_image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $logo = ClientLogo::findOrFail($id);

            /**
             * Update nama perusahaan
             */
            if ($request->filled('company_name')) {
                $logo->company_name = $request->company_name;
            }

            /**
             * Update logo image
             */
            if ($request->hasFile('logo_image_path')) {

                // hapus file lama
                if (
                    $logo->logo_image_path &&
                    Storage::disk('local')->exists($logo->logo_image_path)
                ) {
                    Storage::disk('local')->delete($logo->logo_image_path);
                }

                // generate nama file baru
                $filename = Str::uuid() . '.' .
                    $request->file('logo_image_path')->getClientOriginalExtension();

                // simpan file baru
                $path = $request->file('logo_image_path')->storeAs('logos', $filename, 'public');

                $logo->logo_image_path = $path;
            }

            $logo->save();

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Logo perusahaan berhasil diperbarui',
            //     'data' => $logo
            // ]);

            return redirect()->back()->with('success', 'Logo perusahaan berhasil diperbarui');

        } catch (\Exception $e) {

            // return response()->json([
            //     'message' => $e->getMessage()
            // ], 500);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui logo perusahaan');
        }
    }

    /**
     * Hapus logo perusahaan
     */
    public function destroy($id)
    {
        try {

            $logo = ClientLogo::findOrFail($id);

            // hapus file logo
            if (
                $logo->logo_image_path &&
                Storage::disk('local')->exists($logo->logo_image_path)
            ) {
                Storage::disk('local')->delete($logo->logo_image_path);
            }

            // hapus data database
            $logo->delete();

            //  return response()->json([
            //     'success' => true,
            //     'message' => 'Logo perusahaan berhasil dihapus'
            // ]);

            return redirect()->back()->with('success', 'Logo perusahaan berhasil dihapus');

        } catch (\Exception $e) {

            // return response()->json([
            //     'message' => $e->getMessage()
            // ], 500);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus logo perusahaan');
        }
    }
}
