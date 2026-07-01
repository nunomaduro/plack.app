<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Bookmark;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteBookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $bookmark = $this->route('bookmark');
        assert($bookmark instanceof Bookmark);

        return $bookmark->user_id === $this->user()?->id;
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
