<?php


namespace App\Filters;


class UserFilter extends QueryFilters
{

    /**
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     * @version 1.0.0
     * @since 1.0
     */
    public function statusFilter($value)
    {
        if ($value != 'any') {
            return $this->builder->where('status', $value);
        } else {
            return $this->builder;
        }
    }

    /**
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     * @version 1.0.0
     * @since 1.0
     */
    public function queryFilter($value)
    {
        if(blank($value)) return $this->builder;
        return $this->builder->whereRaw("LOWER(name) LIKE '%" . strtolower($value) . "%'")
            ->orWhere('email', $value)->orWhere('id', get_uid($value));

    }

    /**
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     * @version 1.0.0
     * @since 1.0
     */
    public function roleFilter($value)
    {
        if ($value != 'any') {
            return $this->builder->where('role', $value);
        } else {
            return $this->builder;
        }
    }
}
