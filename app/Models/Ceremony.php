<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Ceremony
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public static function fetchCombo()
    {
        return DB::connection('finger')
            ->table('set_upacara')
            ->selectRaw("CONCAT(tanggal, ' - ', keterangan) AS text, tanggal as value")
            ->orderBy('tanggal', 'DESC')
            ->get();
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $date
     * @param string $format
     * @return boolean
     */
    public static function isValidDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $date
     * @return void
     */
    public static function getFingerTable($date)
    {
        $tableName = 'absensi_harian_' . date('Ym', strtotime($date));

        if (Schema::connection('finger')->hasTable($tableName)) {
            return $tableName;
        }

        return null;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function mustParticipant()
    {
        return json_decode(
            json_encode(
                DB::connection('finger')
                    ->table('set_hari_kerja_pegawai')
                    ->select('nip')
                    ->where(['status_upacara' => 1])
                    ->get()
            )
        , true); 
    }

    /**
     * Undocumented function
     *
     * @param [type] $tableName
     * @param [type] $participants
     * @return void
     */
    public static function updateFlag($tableName, $date, $participants)
    {
        return DB::connection('finger')
            ->table($tableName)
            ->where('tanggal', $date)
            ->whereIn('nip', $participants)
            ->update(['flag_wajib' => 1]);
    }

    public static function participants($tableName, $date)
    {
        return DB::connection('finger')
            ->table($tableName)
            ->where([
                'tanggal' => $date,
                'flag_wajib' => 1
            ]);
    }
}