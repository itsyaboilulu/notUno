<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * model for game settings @see game
 *
 * @param int $gid game id
 */
class gameSettings
{

    /**
     * game settings
     *
     * @var array
     */
    private $settings;

    /**
     * game id
     *
     * @var int
     */
    private $game_id;

    /**
     * model for game settings @see game
     *
     * @param int $gid game id
     */
    function __construct($gid)
    {
        $this->game_id  = $gid;
        $game = game::find($gid);
        $this->settings = ($game->setting) ?
            unserialize($game->setting) :
            $this->standardSettings();
    }

    /**
     * get setting data, if setting isnt set returns base setting from standeredSettings()
     *
     * @param string $key
     * @return mixed
     */
    function __get($key)
    {
        if (isset($this->settings()[$key])) {
            return $this->settings()[$key];
        } else if (isset($this->standardSettings()[$key])) {
            //incase i add settings mid game, stops game breaking
            $this->__Set($key, $this->standardSettings()[$key]);
            return $this->settings()[$key];
        }
        return;
    }

    /**
     * set setting into stored data
     *
     * @param string $key
     * @param string $value
     * @return mixed
     */
    function __Set($key, $value)
    {
        return $this->settings[$key] = $value;
    }

    /**
     * returns the stored settings data, if non stored creates a set
     * from standered list
     *
     * @return array
     */
    public function settings()
    {
        if (!$this->settings) {
            $this->settings = $this->standardSettings();
        }
        return $this->settings;
    }

    /**
     * commit settings into game data
     *
     * @return boolean
     */
    public function save()
    {
        $game = game::find($this->game_id);
        $game->setting = serialize($this->settings());
        return $game->save();
    }

    /**
     * set settings from a passed array
     *
     * @param array $settings
     * @return array
     */
    public function setSettings($settings)
    {
        if (is_array($settings)) {
            $this->settings = $settings;
        }
        return $this->settings;
    }

    /**
     * returns array of standered settings
     *
     * @return array
     */
    private function standardSettings()
    {
        return array(
            'unoDrawPenalty'    => 2,
            'stack'             => 0,
            'drawUntilPlay'     => 0,
            'allowTimeouts'     => 0,
            'timeoutsTime'      => 30,
            'timeoutsDraw'      => 2,
            'extreme7'          => 0,
            'extreme4'          => 0,
            'extreme0'          => 0,
            'extreme9'          => 0,
            'extreme1'          => 0,
            'extreme2'          => 0,
            'extreme6'          => 0
        );
    }

    /**
     * returns array of standered settings
     *
     * @return array
     */
    public function maxSettings()
    {
        return array(
            'unoDrawPenalty'    => 10,
            'stack'             => 1,
            'drawUntilPlay'     => 1,
            'allowTimeouts'     => 1,
            'timeoutsTime'      => 1440,
            'timeoutsDraw'      => 10,
            'extreme7'          => 1,
            'extreme4'          => 1,
            'extreme0'          => 1,
            'extreme9'          => 1,
            'extreme1'          => 1,
            'extreme2'          => 1,
            'extreme6'          => 1
        );
    }
}
