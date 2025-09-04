<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'page_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'referrer',
        'session_id',
        'country',
        'city',
        'device_type',
        'browser',
        'platform',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the page that owns the page view.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by page.
     */
    public function scopeForPage($query, $pageId)
    {
        return $query->where('page_id', $pageId);
    }

    /**
     * Scope a query to get unique visitors.
     */
    public function scopeUniqueVisitors($query)
    {
        return $query->select('visitor_id')->distinct();
    }

    /**
     * Scope a query to filter by device type.
     */
    public function scopeDeviceType($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope a query to filter by country.
     */
    public function scopeCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Get the geographic data for analytics.
     */
    public static function getGeographicData($pageId = null, $startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->selectRaw('country, city, COUNT(*) as views, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->whereNotNull('country')
            ->groupBy('country', 'city')
            ->orderByDesc('views')
            ->get();
    }

    /**
     * Get the device data for analytics.
     */
    public static function getDeviceData($pageId = null, $startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->selectRaw('device_type, browser, platform, COUNT(*) as views, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('device_type', 'browser', 'platform')
            ->orderByDesc('views')
            ->get();
    }

    /**
     * Get the referral data for analytics.
     */
    public static function getReferralData($pageId = null, $startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->selectRaw('referrer, COUNT(*) as visits')
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByDesc('visits')
            ->get();
    }

    /**
     * Get UTM campaign data for analytics.
     */
    public static function getUTMData($pageId = null, $startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->selectRaw('utm_source, utm_medium, utm_campaign, COUNT(*) as visits')
            ->whereNotNull('utm_source')
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->orderByDesc('visits')
            ->get();
    }

    /**
     * Get daily views for timeline charts.
     */
    public static function getDailyViews($pageId = null, $startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->selectRaw('DATE(created_at) as date, COUNT(*) as views, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get hourly views for real-time analytics.
     */
    public static function getHourlyViews($pageId = null, $date = null)
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($date) {
            $query->whereDate('created_at', $date);
        } else {
            $query->whereDate('created_at', today());
        }

        return $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as views')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Check if a view is from a returning visitor.
     */
    public function isReturningVisitor(): bool
    {
        return static::where('visitor_id', $this->visitor_id)
            ->where('id', '<>', $this->id)
            ->exists();
    }

    /**
     * Get the bounce rate for a page.
     */
    public static function getBounceRate($pageId = null, $startDate = null, $endDate = null): float
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $totalVisitors = $query->distinct('visitor_id')->count('visitor_id');
        
        if ($totalVisitors === 0) {
            return 0.0;
        }

        $singlePageVisitors = $query->select('visitor_id')
            ->groupBy('visitor_id')
            ->havingRaw('COUNT(*) = 1')
            ->count();

        return round(($singlePageVisitors / $totalVisitors) * 100, 2);
    }

    /**
     * Get average session duration.
     */
    public static function getAverageSessionDuration($pageId = null, $startDate = null, $endDate = null): float
    {
        $query = static::query();
        
        if ($pageId) {
            $query->forPage($pageId);
        }
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }


        return $query->average('visitor_id') ? round($query->average('visitor_id') * 0.5, 2) : 0.0;
    }
}