<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Ceremony;
use DateTime;
use Illuminate\Http\Request;

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
    public function recaps($date)
    {        
        date_default_timezone_set('Asia/Jakarta');
        
        if (Ceremony::isValidDate($date) && ($tableName = Ceremony::getFingerTable($date)) !== null) {
            $participants = Ceremony::participants($tableName, $date);

            return response()->json([
                'timing' => date('Y-m-d h:i:s a'),
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
                ]
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
    public function participants(Request $request)
    {
        return response()->json([
            'agencies' => Ceremony::agencies($request),
            'participants' => Ceremony::employees($request)
        ], 200);
    }

    public function agenciesReport($date)
    {
        $participants = Ceremony::mustParticipant();

        return response()->json([
            'agencies' => Ceremony::recaps($participants, $date)
        ], 200);
    }

    public function walkoutsReport(Request $request)
    {
        return response()->json([
            'walkouts' => Ceremony::employees($request)
        ], 200);
    }
}
