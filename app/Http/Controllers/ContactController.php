<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class ContactController extends Controller
{
    public function __invoke(Request $request)
    {
        // Honeypot: real visitors never see the "website" field.
        if (filled($request->input('website'))) {
            return redirect(url('/').'#kontakt');
        }

        $data = $request->validateWithBag('contact', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', Rule::in(contact_subjects())],
            'message' => ['required', 'string', 'max:5000'],
            'consent' => ['accepted'],
        ]);

        $message = ContactMessage::create([...$data, 'locale' => app()->getLocale()]);

        // The message is already saved in the admin, so a mail outage loses nothing.
        try {
            Mail::raw(
                collect([
                    'Name: '.$message->name,
                    'E-Mail: '.$message->email,
                    'Telefon: '.($message->phone ?: '-'),
                    'Anliegen: '.($message->subject ?: '-'),
                    '',
                    $message->message,
                ])->implode("\n"),
                fn ($mail) => $mail
                    ->to(ml_setting('links.contact_email', 'stiftung@madrisa.ch'))
                    ->replyTo($message->email, $message->name)
                    ->subject('Kontaktanfrage: '.($message->subject ?: $message->name)),
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect(url('/').'#kontakt')->with('contact_sent', true);
    }
}
