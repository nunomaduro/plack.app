<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\StatusPreset;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteStatusPresetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $statusPreset = $this->route('statusPreset');
        assert($statusPreset instanceof StatusPreset);

        return $statusPreset->user_id === $this->user()?->id;
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
