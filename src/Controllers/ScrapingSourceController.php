<?php
namespace Sdkconsultoria\BlogScraping\Controllers;

use Illuminate\Http\Request;
use Sdkconsultoria\Base\Controllers\ResourceController;
use  Sdkconsultoria\BlogScraping\Models\ScrapingUrl;
/**
 * [class description]
 */
class ScrapingSourceController extends ResourceController
{
    protected $model    = '\Sdkconsultoria\BlogScraping\Models\ScrapingSource';
    protected $view     = 'scraping::scraping-source';
    protected $resource = 'source';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request);

        $model = $this->createOrFind();
        $model->status = $this->model::STATUS_ACTIVE;
        $this->loadData($model, $request);
        $model->created_by = \Auth::user()->id;

        $model->save();

        return redirect()->route($this->resource . '.index')->with('success', __('base::messages.saved'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request);

        $model = $this->findModel($id);
        $this->loadData($model, $request);

        $model->save();

        return redirect()->route($this->resource . '.index')->with('success', __('base::messages.saved'));
    }

    public function scanMenu($seoname)
    {
        // $drive = new \App\Scrape\WantToKnow;
        // $drive->getUrls();
        // return redirect()->route($this->resource . '.show', $seoname)->with('success', __('scraping::attributes.scan_finish'));

        foreach ($this->getDrivers($seoname) as $key => $driver) {
            $drive = new $driver->driver;
            $drive->getUrls();
        }

        return redirect()->route($this->resource . '.show', $seoname)->with('success', __('scraping::attributes.scan_finish'));
    }

    public function scanUrls($seoname)
    {
        foreach ($this->getDrivers($seoname) as $key => $driver) {
            $drive = new $driver->driver;
            $drive->getData();
        }

        return redirect()->route($this->resource . '.show', $seoname)->with('success', __('scraping::attributes.scan_finish'));
    }

    private function getDrivers($seoname)
    {
        $model = $this->findModel($seoname);
        return ScrapingUrl::select('driver')->where('status', ScrapingUrl::STATUS_ACTIVE)->groupBy('driver')->get();
    }
}
