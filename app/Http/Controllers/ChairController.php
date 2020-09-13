<?php

namespace App\Http\Controllers;

use App\Chair;
use App\Http\Helpers\ChairHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ChairController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new ChairHelper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $chairs = Chair::all();

        $this->helper->setChairStatuses(Chair::where('status', 'reserved')->get());

        return view('index')->with('chairs', $chairs);
    }

    /**
     * Szék foglalás
     */
    public function reserve(string $chair_id): RedirectResponse
    {
        // A szék azonosítója alapján megkeressük a széket
        $chair = Chair::find($chair_id);

        // A session ID-t használom az optimistic lock-hoz
        $session_id = Session::getId();

        // Ha a szék státusza jelenleg "szabad", akkor "foglalt"-ra állítjuk
        // és beállítjuk mellé a Session ID-t, hogy később tudjuk ki foglalta le
        if ($chair->status === 'free') {

            $chair->status = 'reserved';
            $chair->reserved_until = date("Y-m-d H:i:s", strtotime("+2 minutes"));
            $chair->reserver_id = $session_id;

        // Ha a szék státusza jelenleg "foglalt"
        } elseif ($chair->status === 'reserved') {
            try {
                // és megegyezik a Session ID-ja azzal, aki most szeretné felszabadítani a széket,
                // akkor "szabad"-ra állítjuk
                if ($chair->reserver_id === $session_id) {

                    $chair->status = 'free';
                    $chair->reserved_until = null;
                    $chair->reserver_id = '';

                // ellenkező esetben hibaüzenet
                } else {
                    throw new Exception('Ezt a széket már más lefoglalta!');
                }
            } catch (Exception $e) {
                Session::flash('message', $e->getMessage());
                Session::flash('alert-class', 'alert-danger');
            }
        }

        // Változások mentése
        $chair->save();

        // Visszairányítás az index oldalra
        return redirect()->action('ChairController@index');
    }

    /**
     * Fizetés
     */
    public function pay(Request $request): RedirectResponse
    {
        // Csak akkor érvényes a fizetés, ha valid email címet adnak meg
        try {
            if ($this->helper->ensureIsValidEmail($request->email)) {

                // Leválogatjuk azokat a foglalt székeket, amelyeket az aktuális Session ID-val foglaltak
                $session_id = Session::getId();
                $reserved_chairs = Chair::where('reserver_id', $session_id)->get();

                $chair_nums_for_email = "";

                foreach ($reserved_chairs as $index => $reserved_chair) {

                    // Az adott székek státuszát "elkelt"-re állítjuk
                    $reserved_chair->status = 'sold';
                    $reserved_chair->save();

                    // Összegyűjtjük az adott székek sorát és számát, hogy az email-ben megjeleníthessük őket
                    if ($index != 0) {
                        $chair_nums_for_email .= ", ";
                    }
                    $chair_nums_for_email .= $reserved_chair->row.". sor ".$reserved_chair->number.". szék";
                }

                // Emailküldés
                $this->helper->sendEmail($request, $chair_nums_for_email);
            }
        } catch (Exception $e) {
            Session::flash('message', $e->getMessage());
            Session::flash('alert-class', 'alert-danger');
        }

        // Visszairányítás az index oldalra
        return redirect()->action('ChairController@index');
    }
}
