<div>
    <table class="td-left" style="border-collapse:collapse;">
        <tbody>
        <tr>
            <td><strong>{{ __('msg.class_teachers_comment') }}</strong></td>
            <td>  {{ $exr->t_comment ?: str_repeat('__', 40) }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('msg.principals_comment') }}</strong></td>
            <td>  {{ $exr->p_comment ?: str_repeat('__', 40) }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('msg.next_term_begins') }}</strong></td>
            <td>{{ date('l\, jS F\, Y', strtotime($s['term_begins'])) }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('msg.next_term_fees') }}</strong></td>
            <td><del style="text-decoration-style: double">{{ __('msg.n') }}</del>{{ $s['next_term_fees_'.strtolower($ct)] }}</td>
        </tr>
        </tbody>
    </table>
</div>
