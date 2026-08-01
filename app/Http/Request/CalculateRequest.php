<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_scheme' => ['nullable', 'string', 'in:undisclosed'],
            'freight_owner' => ['required', 'numeric', 'min:0'],
            'freight_shipper' => ['required', 'numeric', 'min:0'],
            'reimbursable_costs' => ['nullable', 'numeric', 'min:0'],
            'shipowner_status' => ['required', 'string', 'in:siupal,sewa_harta,asing_but,asing_non_but'],
            'pkp_agen' => ['nullable', 'boolean'],
            'pkp_shipowner' => ['nullable', 'boolean'],
            'pkp_shipper' => ['nullable', 'boolean'],
            'subbroker_split_active' => ['nullable', 'boolean'],
            'split_type' => ['required_if:subbroker_split_active,true', 'nullable', 'string', 'in:percentage,fixed'],
            'split_value' => ['required_if:subbroker_split_active,true', 'nullable', 'numeric', 'min:0'],
            'sub_broker_entity' => ['required_if:subbroker_split_active,true', 'nullable', 'string', 'in:corporate,individual'],
        ];
    }

    public function messages(): array
    {
        return [
            'freight_owner.required' => 'Freight owner wajib diisi',
            'freight_owner.numeric' => 'Freight owner harus berupa angka',
            'freight_owner.min' => 'Freight tidak boleh negatif',
            'freight_shipper.required' => 'Freight shipper wajib diisi',
            'freight_shipper.numeric' => 'Freight shipper harus berupa angka',
            'freight_shipper.min' => 'Freight tidak boleh negatif',
            'reimbursable_costs.numeric' => 'Biaya operasional harus berupa angka',
            'reimbursable_costs.min' => 'Biaya operasional tidak boleh negatif',
            'shipowner_status.required' => 'Status shipowner wajib diisi',
            'shipowner_status.in' => 'Status shipowner tidak valid',
            'split_type.required_if' => 'Split wajib diisi saat fitur sub-broker aktif',
            'split_type.in' => 'Tipe split tidak valid',
            'split_value.required_if' => 'Split wajib diisi saat fitur sub-broker aktif',
            'split_value.numeric' => 'Split harus berupa angka',
            'split_value.min' => 'Split tidak boleh negatif',
            'sub_broker_entity.required_if' => 'Jenis entitas sub-broker wajib diisi saat fitur sub-broker aktif',
            'sub_broker_entity.in' => 'Jenis entitas sub-broker tidak valid',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        $data['transaction_scheme'] = $data['transaction_scheme'] ?? 'undisclosed';

        $subbrokerActive = $this->toBoolean($data['subbroker_split_active'] ?? null);
        if (!$subbrokerActive && empty($data['split_type'])) {
            $data['split_type'] = 'percentage';
        }

        foreach (['pkp_agen', 'pkp_shipowner', 'pkp_shipper', 'subbroker_split_active'] as $field) {
            $data[$field] = $this->toBoolean($data[$field] ?? null);
        }

        foreach (['freight_owner', 'freight_shipper', 'reimbursable_costs', 'split_value'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                $data[$field] = (float) $data[$field];
            }
        }

        $this->replace($data);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $data = $this->validated();

            if (($data['subbroker_split_active'] ?? false) && ($data['split_type'] ?? null) === 'percentage') {
                if (isset($data['split_value']) && $data['split_value'] > 100) {
                    $validator->errors()->add('split_value', 'Split tidak boleh melebihi 100%');
                }
            }

            if (empty($data['pkp_agen'])) {
                $data['pkp_agen'] = false;
            }

            if (($data['freight_shipper'] ?? 0) < ($data['freight_owner'] ?? 0)) {
                $validator->errors()->add('freight_shipper', 'Freight shipper harus lebih besar atau sama dengan freight owner');
            }
        });
    }

    private function toBoolean($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }
}
