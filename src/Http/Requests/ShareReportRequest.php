<?php

namespace Facade\Ignition\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Facade\Ignition\Rules\AtLeastOneTabIsSelectedRule;

class ShareReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'report' => 'required',
            'tabs' => 'required|array|min:1',
            'lineSelection' => [],
        ];
    }
}
