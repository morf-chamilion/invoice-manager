<?php

namespace App\Http\Resources\Admin\Page;

use App\Enums\PageStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Page\PageRoutePath;
use App\Services\PageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class PageIndexResource extends JsonResource implements HasDataTableInterface
{
    use HasDataTableTrait;

    /**
     * Transform the resource into an array.
     */
    public function transformRecords($records)
    {
        return collect($records)->map(function ($record) {

            /** @var PageService $pageService */
            $pageService = App::make(PageService::class);

            return [
                $record->id,
                $record->title,
                $this->frontRoute($record),
                $pageService->getTemplateName($record->admin_template) ?? 'None',
                PageStatus::toBadge($record->status),
                $this->actions($record),
            ];
        })->all();
    }

    /**
     * Render resource link.
     */
    private static function frontRoute(Model $resource): string
    {
        if (!$resource) {
            return Blade::render('{{ $title }}', [
                'title' => $resource->slug,
            ]);
        }

        return Blade::render('<a href="{{ $url }}" target="_blank">{{ $title }}</a>', [
            'url' => route(PageService::pageRouteName($resource)),
            'title' => $resource->slug,
        ]);
    }

    /**
     * Render record actions.
     */
    private function actions(
        Model $record,
        $edit = PageRoutePath::EDIT,
        $destroy = PageRoutePath::DESTROY
    ): array {
        return [
            'edit' => Gate::check($edit) ? route($edit, $record) : '',
            'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
        ];
    }
}
