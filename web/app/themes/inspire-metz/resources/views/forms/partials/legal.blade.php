@php($link = '<a href="'.esc_url(get_privacy_policy_url() ?: '#').'" class="underline underline-offset-2">'.esc_html__('confidentialité', 'inspire-metz').'</a>')

<p class="text-xs leading-relaxed text-slate-500">{!! str_replace(':link', $link, esc_html($text)) !!}</p>
