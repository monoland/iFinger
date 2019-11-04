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
        return $d && $d->format($format) === $date && $d->format($format) <= date($format);
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

    /**
     * Undocumented function
     *
     * @param [type] $tableName
     * @param [type] $date
     * @return void
     */
    public static function participants($tableName, $date)
    {
        return DB::connection('finger')
            ->table($tableName)
            ->where([
                'tanggal' => $date,
                'flag_wajib' => 1
            ]);
    }

    /**
     * Undocumented function
     *
     * @param [type] $participants
     * @return void
     */
    public static function agencies($request)
    {
        $participants = "'" . implode("','", array_column($request->participants, 'nip')) . "'";

        return DB::connection('simpeg')
            ->select("SELECT 
                ref_unkerja.nunker AS unker, 
                COUNT(peg_tkerja.nip) AS count FROM peg_tkerja
                LEFT JOIN ref_unkerja ON CONCAT(peg_tkerja.kuntp,peg_tkerja.kunkom,'00000000000') = ref_unkerja.kunker
                WHERE peg_tkerja.nip IN ($participants)
                GROUP BY ref_unkerja.nunker");
    }

    /**
     * Undocumented function
     *
     * @param [type] $participants
     * @return void
     */
    public static function employees($request)
    {
        $participants = "'" . implode("','", array_column($request->participants, 'nip')) . "'";

        return DB::connection('simpeg')
            ->select("SELECT 
                peg_identpeg.nip, 
                CONCAT(IF(gldepan IS NULL or gldepan = '', '', CONCAT(gldepan, '. ')), nama, IF(glblk IS NULL or glblk = '', '', CONCAT(', ', glblk))) AS nama, 
                ref_golruang.ngolru AS gol,
                peg_jakhir.njab AS jabatan,
                ref_eselon.neselon AS esl,
                ref_unkerja.nunker AS unker
                FROM peg_identpeg
                JOIN peg_pakhir ON peg_identpeg.nip = peg_pakhir.nip
                JOIN peg_jakhir ON peg_identpeg.nip = peg_jakhir.nip
                JOIN ref_golruang ON peg_pakhir.kgolru = ref_golruang.kgolru
                JOIN ref_eselon ON peg_jakhir.keselon = ref_eselon.keselon
                JOIN ref_unkerja ON CONCAT(LEFT(peg_jakhir.kunker, 4), '00000000000') = ref_unkerja.kunker
                WHERE peg_identpeg.nip IN ($participants)
                ORDER BY unker, peg_identpeg.nip");
    }

    /**
     * Undocumented function
     *
     * @param [type] $participants
     * @return void
     */
    public static function recaps($employees, $date)
    {
        $participants = "'" . implode("','", array_column($employees, 'nip')) . "'";
        $dynamicName = "absensi_harian_" . date('Ym', strtotime($date));
        
        $recapFingers = DB::connection('finger')
            ->select("SELECT 
                CONCAT(LEFT(kunker, 4), '00000000000') AS unker,
                COUNT(IF(flag_wajib = 1, 1, NULL)) AS wajib,
                COUNT(IF(flag_apel = 1 AND tel_apel IS NULL, 1, NULL)) AS hadir,
                COUNT(IF(flag_apel = 1 AND tel_apel IS NOT NULL, 1, NULL)) AS telat,
                COUNT(IF(flag_apel = 0 AND stat_ijn IS NOT NULL, 1, NULL)) AS ijin,
                COUNT(IF(flag_apel = 0 AND stat_ijn IS NULL, 1, NULL)) AS mangkir
                FROM $dynamicName 
                WHERE tanggal = '$date' 
                AND nip IN ($participants)
                GROUP BY unker");
        
        $unkers = "'" . implode("','", array_column($recapFingers, 'unker')) . "'";

        $agencies = DB::connection('simpeg')
            ->select("SELECT
                kunker AS unker, 
                nunker AS unit_kerja
                FROM ref_unkerja
                WHERE kunker IN($unkers)
                ORDER BY unker");
        
        return (new static)->array_fixer($recapFingers, $agencies);
    }

    /**
     * Undocumented function
     *
     * @param array $array1
     * @param array $array2
     * @return void
     */
    private function array_fixer(array $array1, array $array2)
    {
        $merged = [];
        
        foreach ($array1 as $key => $value) {
            $merged[$key] = array_merge((array) $value, (array) $array2[$key]);
        }

        return $merged;
    }
}