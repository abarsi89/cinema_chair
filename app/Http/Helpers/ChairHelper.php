<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

/**
 * A ChairController segéd metódusait tartalmazó osztály
 */
class ChairHelper
{
    /**
     * Minden index kérés előtt tisztázza a székek státuszát.
     * Visszaírja "szabad" státuszúra azokat, amelyeknek lejárt a forglaltsága.
     */
    public function setChairStatuses(Collection $chairs): void
    {
        $now = date("Y-m-d H:i:s");

        foreach ($chairs as $chair) {;

            if ($chair->reserved_until < $now) {
                $chair->status = 'free';
                $chair->reserved_until = null;
                $chair->reserver_id = '';

                $chair->save();
            }
        }
    }

    /**
     * Emailküldés fizetésképpen
     */
    public function sendEmail(Request $request, string $chairs): void
    {
        $data = [
            'email' => $request->email,
            'chairs' => $chairs,
        ];

        Mail::send(['text'=>'mail'], $data, function($message) use ($request) {
            $message->to($request->email, 'Néző')->subject('Sikeres fizetés!');
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });

        Session::flash('message', 'Email elküldve. Nézd meg a postafiókod!');
        Session::flash('alert-class', 'alert-success');
    }

    /**
     * Email cím ellenőrzése
     */
    public function ensureIsValidEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                sprintf(
                    'A(z) "%s" nem valid email cím!',
                    $email
                )
            );

            return false;
        }
        return true;
    }
}
