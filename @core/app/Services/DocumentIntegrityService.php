<?php

namespace App\Services;

use App\Cause;
use Illuminate\Support\Facades\DB;

/**
 * Tamper-evidence for medical documents.
 *
 * The SHA-256 of every medical document is sealed at approval time.
 * "Verify" recomputes the hash of the file currently on disk and compares:
 *   match    → document untouched since sealing
 *   mismatch → file was modified after approval (tamper alert)
 */
class DocumentIntegrityService
{
    public const ALGO = 'sha256';

    /** Resolve the absolute path of a campaign's medical document */
    public static function resolveFile(Cause $cause): ?string
    {
        $ref = trim((string) $cause->medical_document);

        if ($ref === '') {
            return null;
        }

        if (ctype_digit($ref)) {
            /* reference to media_uploads table */
            $row = DB::table('media_uploads')->find((int) $ref);
            if (!$row || empty($row->path)) {
                return null;
            }
            $path = public_path('assets/uploads/' . ltrim($row->path, '/\\'));

            return file_exists($path) ? $path : null;
        }

        $path = public_path(ltrim($ref, '/\\'));

        return file_exists($path) ? $path : null;
    }

    /** Compute SHA-256 over the stored file; null when there is nothing to hash */
    public static function computeHash(Cause $cause): ?string
    {
        $file = self::resolveFile($cause);

        return $file ? hash_file(self::ALGO, $file) : null;
    }

    /**
     * Seal: compute and persist the document hash (called at approval / admin save).
     * Re-sealing is deliberate — it re-baselines the trust anchor at that moment.
     */
    public static function stamp(Cause $cause): ?string
    {
        $hash = self::computeHash($cause);
        if ($hash === null) {
            return null;
        }

        $cause->forceFill([
            'document_hash' => $hash,
            'document_hashed_at' => now(),
            'document_verified_at' => now(),
        ])->save();

        return $hash;
    }

    /**
     * Verify: recompute and compare against the seal.
     *
     * @return array{status: string, stored: ?string, computed: ?string}
     *         status ∈ no_doc|unsealed|match|mismatch|missing_file
     */
    public static function verify(Cause $cause): array
    {
        if (empty(trim((string) $cause->medical_document))) {
            return ['status' => 'no_doc', 'stored' => null, 'computed' => null];
        }

        if (self::resolveFile($cause) === null) {
            return ['status' => 'missing_file', 'stored' => $cause->document_hash, 'computed' => null];
        }

        $computed = self::computeHash($cause);
        $stored = $cause->document_hash;

        if (!$stored) {
            return ['status' => 'unsealed', 'stored' => null, 'computed' => $computed];
        }

        return [
            'status' => hash_equals($stored, (string) $computed) ? 'match' : 'mismatch',
            'stored' => $stored,
            'computed' => $computed,
        ];
    }
}
