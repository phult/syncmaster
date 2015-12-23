package com.luong.syncmaster.network;

import org.json.JSONObject;

/**
 * Created by phuluong on 12/19/15.
 */
public interface IOListener {

    public static int CONNECTION_STATUS_CONNECTED = 1;
    public static int CONNECTION_STATUS_DISCONNECTED = 0;
    public static int CONNECTION_STATUS_ERROR = -1;

    void onIOMessage(JSONObject msg);

    void onIOStateChange(String status);
}
