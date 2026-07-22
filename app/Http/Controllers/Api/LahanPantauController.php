<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LahanPantauRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LahanPantauController extends Controller
{
    public function __construct(
        protected LahanPantauRepositoryInterface $lahanRepo
    ) {}

    /**
     * GET /api/v1/lahan-pantau
     */
    public function index(): JsonResponse
    {
        try {
            $lahans = $this->lahanRepo->getAll();

            return response()->json([
                'success' => true,
                'data' => $lahans,
                'total' => count($lahans),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching lahan pantau',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/lahan-pantau/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $lahan = $this->lahanRepo->getWithDevices($id);

            return response()->json([
                'success' => true,
                'data' => $lahan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lahan pantau tidak ditemukan',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * POST /api/v1/lahan-pantau
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_lahan' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $lahan = $this->lahanRepo->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Lahan pantau berhasil dibuat',
                'data' => $lahan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating lahan pantau',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/v1/lahan-pantau/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_lahan' => 'sometimes|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $lahan = $this->lahanRepo->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Lahan pantau berhasil diupdate',
                'data' => $lahan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lahan pantau',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/v1/lahan-pantau/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->lahanRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Lahan pantau berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lahan pantau',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
