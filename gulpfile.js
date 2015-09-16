var elixir = require('laravel-elixir');

elixir(function(mix) {
 mix.phpUnit(['resources/**/*.json', 'tests/**/*.php']);
});
