<?php

namespace Razorpay\Api;

class Subscription extends Entity
{
    public function create($attributes = array())
    {
        return parent::create($attributes);
    }

    public function fetch($id)
    {
        return parent::fetch($id);
    }

    public function all($options = array())
    {
        return parent::all($options);
    }
    
    public function cancel($subscriptions_id,$attributes = array())
    {
        $relativeUrl = $this->getEntityUrl() . $subscriptions_id . '/cancel';

        return $this->request('POST', $relativeUrl, $attributes);
    }

    public function createAddon($attributes = array())
    {
        $relativeUrl = $this->getEntityUrl() . $this->id . '/addons';

        return $this->request('POST', $relativeUrl, $attributes);
    }
}
