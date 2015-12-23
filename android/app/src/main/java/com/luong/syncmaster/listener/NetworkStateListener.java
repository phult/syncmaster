package com.luong.syncmaster.listener;

/**
 * Created by phuluong on 12/19/15.
 */
public interface NetworkStateListener {
    static final String TYPE_CONNECTED = "connected";
    static final String TYPE_DISCONNECTED = "disconnected";
    void onNetworkStateChange(String type, Object args);
}
