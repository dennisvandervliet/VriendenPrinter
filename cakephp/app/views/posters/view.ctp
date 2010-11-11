<? foreach($poster["Example"] as $example): ?>
<a href="/orders/create/<?= $example["id"]?>"><img src="/posters/img/example/<?= $example["id"]?>"></a>
<? endforeach; ?>