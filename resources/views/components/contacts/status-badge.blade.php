@props(['status'])

<x-ui.badge variant="outline" :class="$status->badgeClass()">{{ $status->label() }}</x-ui.badge>
