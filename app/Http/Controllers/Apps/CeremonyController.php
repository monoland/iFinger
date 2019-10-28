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
        // 
    }
}
