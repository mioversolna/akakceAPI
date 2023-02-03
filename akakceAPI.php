<?php

class akakceAPI
{
    public $marketPlaceList = array("N11", "Pttavm", "Trendyol", "Pazarama", "ÇiçekSepeti", "Amazon Türkiye", "Avansas", "Hepsiburada.com", "Turkcell", "troy e-store", "Morhipo", "Teknosa.com", "gurgencler.com.tr");
    public $requestURL;
    public $redirectedURL;
    public $result;

    /**
     * @param string $url akakce page url, which api gets data
     * @param array $extraHeaders adds extra headers to request, not neccessary
     * @return void
     */
    public function AA_CurlConnection($url, $extraHeaders = array())
    {
        $this->requestURL = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36',
        ), $extraHeaders));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 00);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $this->result = curl_exec($ch);
        $this->redirectedURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return true;
    }

    /**
     * @param string $query searched product or category title
     * @param integer $resultLength the count of the returning result items, default: 24, not neccessary
     * @param string $sort change the sort of the result list, default: null, not neccessary
     * @return void
     */
    public function AA_SearchProduct($query, $resultLength = 24, $sort = null)
    {
        $pageCount = ($resultLength > 24 ? ceil($resultLength / 24) : 1);
        $sortList = array("bestSeller" => 1, "minPrice" => 2, "maxPrice" => 4, "maxRating" => 7, "new" => 3);
        $parsedResult = array();
        for ($i = 1; $i <= $pageCount; $i++) {
            $this->AA_CurlConnection("https://www.akakce.com/arama/?q=" . urlencode($query) . ($sort !== null && isset($sortList[$sort]) ? "&s=" . $sort : null) . "&p=" . $i);
            $doc = new DOMDocument();
            $doc->loadHTML($this->result);
            $xpath = new DOMXPath($doc);
            $resultList = $xpath->query("//ul");
            if ($resultList->length > 0 && (isset($resultList[1]) || isset($resultList[3]))) {
                foreach ($resultList[($this->requestURL == $this->redirectedURL ? 1 : 3)]->childNodes as $node) {
                    if ($resultLength > 0) {
                        $priceSpan = $xpath->query("./span[@class='pb_v8']", $node->childNodes[0]->childNodes[1])[0]->childNodes[0];
                        if ($priceSpan->childNodes->length > 0) {
                            $price = (float)str_replace(array(" TL ", ".", " ", ","), array("", "", "", "."), $priceSpan->childNodes[0]->textContent . $priceSpan->childNodes[1]->textContent);
                            $parsedResult[] = array(
                                "resultID" => $node->attributes->getNamedItem("data-pr")->value,
                                "itemBrand" => $node->attributes->getNamedItem("data-mk")->value,
                                "itemTitle" => $node->childNodes[0]->childNodes[1]->childNodes[0]->childNodes[0]->textContent,
                                "minPriceFloat" => $price,
                                "minPriceFormat" => number_format($price, 2, ",", ".")
                            );
                            $resultLength--;
                        }
                    }
                }
                return $parsedResult;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string|integer $productID unique productID in akakce database, this is coming from AA_SearchProduct function
     * @param boolean $groupMarketplaces grouping the marketplaces shows minimum priced listing in marketplace, default: false, not neccessary
     * @return void
     */
    public function AA_GetProductDetail($productID, $groupMarketplaces = false)
    {
        $parsedResult = array();
        $this->AA_CurlConnection("https://api.akakce.com/p/?" . $productID);
        $doc = new DOMDocument();
        $doc->loadHTML($this->result);
        $xpath = new DOMXPath($doc);
        $resultList = $xpath->query("//ul");
        if ($resultList->length > 0 && isset($resultList[3])) {
        }
        $counter = 0;
        foreach ($resultList[3]->childNodes as $node) {
            $marketplaceDetail = $xpath->query("./span[@class='w_v8']", $node->childNodes[0])[2]->childNodes[0]->childNodes[0];
            if ((!$marketplaceDetail->attributes && in_array($marketplaceDetail->textContent, $this->marketPlaceList) || $marketplaceDetail->attributes && in_array($marketplaceDetail->attributes->getNamedItem("alt")->value, $this->marketPlaceList))) {
                $parsedResult["brand"] = $xpath->query("//a[@class='mk']")[0]->textContent;
                $parsedResult["title"] = $xpath->query("//div[@class='pdt_v8']")[0]->childNodes[1]->textContent;
                $priceSpan = $xpath->query("./span[@class='pb_v8']/span[@class='pt_v8']", $node->childNodes[0])[0];
                $price = (float)str_replace(array(" TL ", ".", " ", ","), array("", "", "", "."), $priceSpan->childNodes[0]->textContent . $priceSpan->childNodes[1]->textContent);
                $marketplaceName = ($marketplaceDetail->attributes ? $marketplaceDetail->attributes->getNamedItem("alt")->value : $marketplaceDetail->textContent);
                if (!$groupMarketplaces || (isset($parsedResult["priceList"][$marketplaceName]) && $parsedResult["priceList"][$marketplaceName]["priceFloat"] > $price) || !isset($parsedResult["priceList"][$marketplaceName])) {
                    $parsedResult["priceList"][$groupMarketplaces ? $marketplaceName : $counter] = array(
                        "marketplaceName" => $marketplaceName,
                        "marketplaceImg" => $marketplaceDetail->attributes ? $marketplaceDetail->attributes->getNamedItem("data-src")->value : null,
                        "marketplaceLink" => $marketplaceDetail->attributes ? "https://api.akakce.com/cl/" . explode("/", $node->childNodes[0]->attributes->getNamedItem("href")->value)[2] : null,
                        "priceFloat" => $price,
                        "priceFormat" => number_format($price, 2, ",", "."),
                    );
                }
            }
            $counter++;
        }
        return $parsedResult;
    }
}
