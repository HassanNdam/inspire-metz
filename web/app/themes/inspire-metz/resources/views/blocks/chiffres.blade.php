<div class="container-content">
  <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($block['chiffres'] ?: [] as $index => $chiffre)
      <li class="stat-card stat-card-{{ $index % 3 }}">
        <p class="text-stat font-medium">{{ $chiffre['valeur'] }}</p>
        <p class="mt-auto text-lead-lg">{{ $chiffre['libelle'] }}</p>
      </li>
    @endforeach
  </ul>
</div>
