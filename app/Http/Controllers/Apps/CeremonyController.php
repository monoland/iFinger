<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Ceremony;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CeremonyController extends Controller
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        return view('dashboard');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function events(Request $request)
    {
        return response()->json([
            'events' => Ceremony::fetchCombo()
        ], 200);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $date
     * @return void
     */
    public function recaps(Request $request, $date)
    {        
        if (Ceremony::isValidDate($date) && ($tableName = Ceremony::getFingerTable($date)) !== null) {
            $employees = Ceremony::mustParticipant();

            Ceremony::updateFlag($tableName, $date, $employees);

            $participants = Ceremony::participants($tableName, $date);

            return response()->json([
                'present' => (clone $participants)->select('nip')->where('flag_apel', 1)->whereNull('tel_apel')->get(),
                'late' => (clone $participants)->select('nip')->where('flag_apel', 1)->whereNotNull('tel_apel')->get(),
                'walkout' => (clone $participants)->select('nip')->where('flag_apel', 0)->whereNull('stat_ijn')->get(),
                'permit' => [
                    'data' => (clone $participants)->select('nip')->where('flag_apel', 0)->whereNotNull('stat_ijn')->get(),
                    'details' => (clone $participants)
                        ->join('ref_ijin_new', "$tableName.stat_ijn", '=', 'ref_ijin_new.kode')
                        ->select("$tableName.stat_ijn", 'ref_ijin_new.namaijin')
                        ->selectRaw("COUNT($tableName.stat_ijn) as qty")
                        ->whereNotNull("$tableName.stat_ijn")
                        ->where("$tableName.flag_apel", 0)
                        ->groupBy("$tableName.stat_ijn", 'ref_ijin_new.namaijin')
                        ->get()
                ],
            ], 200);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $date
     * @return void
     */
    public function participants(Request $request, $date)
    {
        $participants = implode(',', array_column($request->participants, 'nip'));

        return response()->json([
            'agencies' => DB::connection('simpeg')
                ->select("SELECT ref_unkerja.nunker AS unker, COUNT(peg_tkerja.nip) AS count FROM peg_tkerja
                    LEFT JOIN ref_unkerja ON CONCAT(peg_tkerja.kuntp,peg_tkerja.kunkom,'00000000000') = ref_unkerja.kunker
                    WHERE peg_tkerja.nip IN ($participants)
                    GROUP BY ref_unkerja.nunker"),
            
            'participants' => DB::connection('simpeg')
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
                WHERE peg_identpeg.nip IN ($participants)")
        ], 200);
    }
}
