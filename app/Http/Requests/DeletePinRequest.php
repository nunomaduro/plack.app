<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Pin;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeletePinRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pin = $this->route('pin');
        assert($pin instanceof Pin);

        return $pin->user_id === $this->user()?->id;
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
