@foreach($nodes as $node)
@php
    $model = $node['item']['_model'] ?? null;
    $inst = null;
    try { $inst = $model ? $model->instance() : null; } catch (\Throwable $e) { $inst = null; }
    $title = $inst->post_title ?? $model->title ?? $node['item']['title'] ?? 'Menu';
    $metaUrl = null; $target = null;
    try { $metaUrl = $model->meta->_menu_item_url ?? null; } catch (\Throwable $e) {}
    try { $target = $model->meta->_menu_item_target ?? null; } catch (\Throwable $e) {}
    $slug = $inst->post_name ?? $model->post_name ?? null;
    $href = $metaUrl ?: $slug ?: '#';
@endphp
<li>
    <a class="menu-item" href="{{ $href }}" @if($target === '_blank') target="_blank" @endif>{{ $title }}</a>
    @if(!empty($node['children']))
        <ul>
            @include('components.menu-nodes', ['nodes' => $node['children']])
        </ul>
    @endif
</li>
@endforeach
