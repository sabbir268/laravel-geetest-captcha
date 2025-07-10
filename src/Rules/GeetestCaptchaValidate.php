<?php

namespace Salahhusa9\GeetestCaptcha\Rules;

use Illuminate\Contracts\Validation\Rule;
use Salahhusa9\GeetestCaptcha\Facades\GeetestCaptcha;

class GeetestCaptchaValidate implements Rule
{
    /**
     * Determine if the validation rule passes.
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
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.geetest_captcha_fail') ?: 'The GeeTest captcha verification failed.';
    }
}
