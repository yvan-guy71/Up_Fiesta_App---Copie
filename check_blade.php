<?php
$file = file_get_contents('resources/views/welcome.blade.php');
$lines = file('resources/views/welcome.blade.php');

// Count directives
preg_match_all('/\s*@if\s*\(/', $file, $if_open);
preg_match_all('/\s*@else/', $file, $else);
preg_match_all('/\s*@elseif\s*\(/', $file, $elseif);
preg_match_all('/\s*@endif/', $file, $if_close);

preg_match_all('/\s*@foreach\s*\(/', $file, $foreach_open);
preg_match_all('/\s*@endforeach/', $file, $foreach_close);

preg_match_all('/\s*@forelse\s*\(/', $file, $forelse_open);
preg_match_all('/\s*@endforelse/', $file, $forelse_close);

preg_match_all('/\s*@auth/', $file, $auth_open);
preg_match_all('/\s*@endauth/', $file, $auth_close);

preg_match_all('/\s*@guest/', $file, $guest_open);
preg_match_all('/\s*@endguest/', $file, $guest_close);

echo "IF statements: " . count($if_open[0]) . " open, " . count($if_close[0]) . " close\n";
echo "FOREACH: " . count($foreach_open[0]) . " open, " . count($foreach_close[0]) . " close\n";
echo "FORELSE: " . count($forelse_open[0]) . " open, " . count($forelse_close[0]) . " close\n";
echo "AUTH: " . count($auth_open[0]) . " open, " . count($auth_close[0]) . " close\n";
echo "GUEST: " . count($guest_open[0]) . " open, " . count($guest_close[0]) . " close\n";
echo "\nLine count: " . count($lines) . "\n";

// Try to find unclosed blocks
foreach ($lines as $i => $line) {
    if (preg_match('/\s*@if\s*\(/', $line)) {
        echo "Line " . ($i+1) . ": @if found\n";
    }
    if (preg_match('/\s*@foreach\s*\(/', $line)) {
        echo "Line " . ($i+1) . ": @foreach found\n";
    }
    if (preg_match('/\s*@forelse\s*\(/', $line)) {
        echo "Line " . ($i+1) . ": @forelse found\n";
    }
}
?>
