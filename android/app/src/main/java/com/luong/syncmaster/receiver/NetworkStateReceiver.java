package com.luong.syncmaster.receiver;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

import com.luong.syncmaster.listener.NetworkStateListener;

public class NetworkStateReceiver extends BroadcastReceiver {
    private NetworkStateListener networkStateListener;

    public NetworkStateReceiver(NetworkStateListener networkStateListener) {
        this.networkStateListener = networkStateListener;
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if (intent.getExtras() != null) {
            NetworkInfo networkInfo = (NetworkInfo) intent.getExtras().get(ConnectivityManager.EXTRA_NETWORK_INFO);
            if (networkInfo != null && networkInfo.getState() == NetworkInfo.State.CONNECTED) {
                networkStateListener.onNetworkStateChange(NetworkStateListener.TYPE_CONNECTED, null);
            } else if (intent.getBooleanExtra(ConnectivityManager.EXTRA_NO_CONNECTIVITY, Boolean.FALSE)) {
                networkStateListener.onNetworkStateChange(NetworkStateListener.TYPE_DISCONNECTED, null);
            }
        }
    }
}
