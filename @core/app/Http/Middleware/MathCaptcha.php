<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MathCaptcha
{
    /**
     * Simple math captcha for authentication forms.
     * GET: generates a fresh question in session for the form to render.
     * POST: validates the submitted answer against the stored result.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            $expected = session('math_captcha_result');
            $answer = $request->input('captcha_answer');

            if ($expected === null || $answer === null || (int) $answer !== (int) $expected) {
                /* keep the same question so the user can retry */

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'msg' => __('Security check failed — please solve the math question correctly'),
                        'type' => 'danger',
                        'status' => 'not_ok',
                    ]);
                }

                return redirect()
                    ->back()
                    ->withInput($request->only('username'))
                    ->with(['msg' => __('Security check failed — please solve the math question correctly'), 'type' => 'danger']);
            }
        } else {
            $this->regenerate();
        }

        return $next($request);
    }

    private function regenerate(): void
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session([
            'math_captcha_a' => $a,
            'math_captcha_b' => $b,
            'math_captcha_result' => $a + $b,
        ]);
    }
}
