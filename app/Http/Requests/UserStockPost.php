<?php

namespace App\Http\Requests;

use App\Egg\Models\UserStock;
use Illuminate\Foundation\Http\FormRequest;

class UserStockPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cate1' => 'bail|required|max:10',
            'code' => 'bail|required|exists:egg_stocks',
            'cost' => 'bail|required|numeric|min:0',
            'hold_num' => 'bail|required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'cate1.*' => '请输入不超过10个字的板块名',
            'code.*' => '基金代码不存在',
            'cost.*' => '成本价需大于等于0',
            'hold_num.*' => '成本价需大于等于0',
        ];
    }
}
