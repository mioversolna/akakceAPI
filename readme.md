# <p align="center"><img src="./readme_files/Akakce.svg" style="background-color: white; margin-right: 10px; border-radius: 10px" width="200px" alt="akakçe api logo"><span>API</span></p>

Yasal olarak "akakçe" tarafından sağlanan bir api ve dokümantasyon değildir, bilgilendirme ve eğitim amaçlıdır. 

## Fonksiyonlar

1- **Bağlantı Kurma** (AA_CurlConnection)
---
- php cURL bağlantısı
- Özel User-Agent
- Özel "header" verisi ekleme
- Hızlı bağlantı ve listeleme
- Kolay düzenlenebilir kod yapısı

> ## Örnek Kod

### Varsayılan İstek
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;
$api->AA_CurlConnection("https://www.akakce.com/arama/?q=256gb+ssd&p=0");
?>
```

### Özel "header" İsteği
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;
$api->AA_CurlConnection("https://www.akakce.com/arama/?q=256gb+ssd&p=0", array(
    "Cookie: sid=3456789098976564",
));
?>
```

---

2- **Ürün arama** (AA_SearchProduct)
---
- Ürünün başlığı
- Ürünün markası
- En düşük fiyat
- Ürün fiyatının formatlanmış ve float hali
- Özel adetli sonuç
- Liste sıralama (çok satan, puan, yeni ürün, fiyat)

> ## Örnek Kod

### Varsayılan İstek
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;
print_r($api->AA_SearchProduct("256gb ssd")); //24 adet yanlızca ilk arama sayfasındaki sonucu getirir
?>
```

### Özel Sonuç Sayısı (default: 24)
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;
print_r($api->AA_SearchProduct("256gb ssd", 50)); //50 adet sonuç getirir, 50 adede ulaşana kadar sayfaları gezer
?>
```

### Özel Sıralama (default: null)
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;

print_r($api->AA_SearchProduct("256gb ssd", 50, "bestSeller")); //Ürünleri çok satana göre sıralar

print_r($api->AA_SearchProduct("256gb ssd", 50, "minPrice")); //Ürünleri fiyatlarına göre ucuzdan pahalıya sıralar

print_r($api->AA_SearchProduct("256gb ssd", 50, "maxPrice")); //Ürünleri fiyatlarına göre pahalıdan ucuza sıralar

print_r($api->AA_SearchProduct("256gb ssd", 50, "maxRating")); //Ürünleri puanlarına göre sıralar

print_r($api->AA_SearchProduct("256gb ssd", 50, "new")); //Ürünleri eklenme tarihine göre sıralar
?>
```

---

3- **Ürün Detayı** (AA_GetProductDetail)
---
- Ürünün başlığı
- Ürünün markası
- Ürünün satıldığı yerlerin listesi
- Satılan pazaryerinin adı (ör. Trendyol)
- Satılan pazaryerinin logosu
- Ürünün pazaryerindeki linki
- Özel pazaryeri filtrelemesi
- Pazaryerlerini gruplama özelliği, (ör. n11 üzerindeki en düşük fiyat)

> #### AA_SearchProduct fonsiyonundaki listeden dönen "resultID" ile sorgulama yapar

> ## Örnek Kod

### Varsayılan İstek
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;
print_r($api->AA_GetProductDetail("1911190850")); //resultID string olarak gönderilebilir

print_r($api->AA_GetProductDetail(1911190850)); //resultID integer olarak gönderilebilir
?>
```

### Pazaryeri Gruplaması (default: false)
```php
<?php
include("akakceAPI.php");

$api = new akakceAPI;
print_r($api->AA_GetProductDetail("1911190850", true)); //Pazaryerleri gruplaması açık

print_r($api->AA_GetProductDetail(1911190850, false)); //Pazaryeri gruplaması kapalı
?>
```

---
---

4- **API**
---

> #### AA_CurlConnection Parameters

```
$url | type: string | default: X | required: true | desc: akakce page url, which api gets data

$extraHeaders | type: array | default: array() | required: false | desc: adds extra headers to request
```

> #### AA_SearchProduct Parameters

```
$query | type: string | default: X | required: true | desc: searched product or category title

$resultLength | type: integer | default: 24 | required: false | desc: the count of the returning result items

$sort | type: string | default: null | required: false | desc: change the sort of the result list
```

> #### AA_GetProductDetail Parameters

```
$productID | type: string||integer | default: X | required: true | desc: unique productID in akakce database, this is coming from AA_SearchProduct function

$groupMarketplaces | type: boolean | default: false | required: false | desc: grouping the marketplaces shows minimum priced listing in marketplace
```

<div>
    &nbsp;
    &nbsp;
    &nbsp;
    <p align="center">
        <img src="http://moletti.com.tr/cdn/mioversolna/mioversolna_never_ever.png" width="25px">
        &nbsp;
        <span>never ever</span>
        <br>
        <span>mioversolna</span>
    </p>
</div>
