package com.luong.syncmaster.listener;

/**
 * Created by phuluong on 12/19/15.
 */
public interface SyncEventListener {
    static final String TYPE_IN_SMS = "in_sms";
    static final String TYPE_IN_CALL = "in_call";
    static final String TYPE_OUT_SMS = "out_sms";
    static final String TYPE_OUT_CALL = "out_call";
    void onEvent(String type, Object args);
}
