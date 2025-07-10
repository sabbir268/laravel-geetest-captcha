<?php

namespace Salahhusa9\GeetestCaptcha\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;
use Salahhusa9\GeetestCaptcha\Facades\GeetestCaptcha;

/**
 * Modern ValidationRule for Laravel 10+ with backward compatibility
 */
class GeetestCaptchaRule implements ValidationRule, Rule
{
    /**
     * Run the validation rule (Laravel 10+).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!GeetestCaptcha::validate($value)) {
            $fail('The GeeTest captcha verification failed.');
        }
    }

    /**
     * Determine if the validation rule passes (Laravel 6-9).
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return GeetestCaptcha::validate($value);
    }

    /**
     * Get the validation error message (Laravel 6-9).
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.geetest_captcha_fail') ?: 'The GeeTest captcha verification failed.';
    }
}
