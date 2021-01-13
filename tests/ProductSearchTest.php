<?php

class ProductSearchTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicHttpRequestWithoutParameteres()
    {
        $response = $this->get('/search');
        $this->assertEquals(400, $this->response->status());
        $response->seeJson([
            'error' => 'Missing parameters',
        ]);
    }

    public function testBasicHttpRequestWithEmptyKeywords()
    {
        $response = $this->get('/search?keywords=');
        $this->assertEquals(400, $this->response->status());
        $response->seeJson([
            'error' => 'Keywords parameter can not be empty',
        ]);
    }

    public function testBasicHttpRequestWithWrongMaxPrice()
    {
        $response = $this->get('/search?keywords=mac&price_max=0');
        $this->assertEquals(400, $this->response->status());
        $response->seeJson([
            'error' => 'Max price must be numeric and greater than 0',
        ]);
    }

    public function testBasicHttpRequestWithWrongMinPrice()
    {
        $response = $this->get('/search?keywords=mac&price_min=bad');
        $this->assertEquals(400, $this->response->status());
        $response->seeJson([
            'error' => 'Min price must be numeric and greater than or equal to 0',
        ]);
    }

    public function testBasicHttpRequestWithMinPriceGreaterThanMaxPrice()
    {
        $response = $this->get('/search?keywords=mac&price_min=1000&price_max=100');
        $this->assertEquals(400, $this->response->status());
        $response->seeJson([
            'error' => 'Max price Must be greater than min price',
        ]);
    }

    public function testBasicHttpRequestWithUnknownSorting()
    {
        $response = $this->get('/search?keywords=mac&price_min=100&price_max=1000&sorting=distance');
        $this->assertEquals(400, $this->response->status());
        $response->seeJson([
            'error' => 'Sorting allowed values are (default and price_asc)',
        ]);
    }

    public function testBasicHttpRequestWithKeywordsOnly()
    {
        $response = $this->get('/search?keywords=mac');
        $this->assertEquals(200, $this->response->status());
    }

    public function testBasicHttpRequestWithAllParams()
    {
        $response = $this->get('/search?keywords=mac&price_min=100&price_max=1000&sorting=default');
        $this->assertEquals(200, $this->response->status());
    }
}
