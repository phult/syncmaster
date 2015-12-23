package com.luong.syncmaster.network;

import android.widget.Toast;

import org.json.JSONObject;

import java.net.MalformedURLException;
import java.net.URISyntaxException;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;

import io.socket.client.IO;
import io.socket.client.Socket;
import io.socket.emitter.Emitter;


/**
 * Created by phuluong on 12/19/15.
 */
public class IOService implements Emitter.Listener {

    public static int USER_ID = 1;
    static String CONNECTION_STRING = "http://10.0.2.2:2000/";
    private static IOService instance;
    private Socket socket;
    private final List<IOListener> listeners = new ArrayList<>();

    private void start() {
        try {
            IO.Options connectionOpts = new IO.Options();
            connectionOpts.query = "userId=" + USER_ID + "&extra=type&type=device-app";
            connectionOpts.forceNew = true;
            connectionOpts.reconnection = true;
            socket = IO.socket(CONNECTION_STRING, connectionOpts);
            socket.on("message", this).on(Socket.EVENT_CONNECT, new Emitter.Listener() {
                @Override
                public void call(Object... args) {
                    for (IOListener listener : listeners) {
                        if (listener != null) {
                            listener.onIOStateChange(Socket.EVENT_CONNECT);
                        }
                    }
                }
            }).on(Socket.EVENT_DISCONNECT, new Emitter.Listener() {
                @Override
                public void call(Object... args) {
                    for (IOListener listener : listeners) {
                        if (listener != null) {
                            listener.onIOStateChange(Socket.EVENT_DISCONNECT);
                        }
                    }
                }
            }).on(Socket.EVENT_CONNECT_ERROR, new Emitter.Listener() {
                @Override
                public void call(Object... args) {
                    for (IOListener listener : listeners) {
                        if (listener != null) {
                            listener.onIOStateChange(Socket.EVENT_CONNECT_ERROR);
                        }
                    }
                }
            });
            socket.connect();
        } catch (URISyntaxException e) {
            e.printStackTrace();
        }
    }

    public void stop() {
        if (socket != null && socket.connected()) {
            socket.disconnect();
        }
    }

    public boolean addNetworkListener(IOListener listener) {
        boolean retval = false;
        if (!listeners.contains(listener)) {
            listeners.add(listener);
            retval = true;
        }
        return retval;
    }

    public boolean removeNetworkListener(IOListener listener) {
        boolean retval = false;
        if (listeners.contains(listener)) {
            listeners.remove(listener);
            retval = true;
        }
        return retval;
    }

    public static IOService getInstance() {
        if (instance == null) {
            instance = new IOService();
        }
        if (!instance.isRunning()) {
            instance.start();
        }
        return instance;
    }

    public void sendMessage(String type, JSONObject content) {
        if (!socket.connected()) {
            socket.connect().emit(type, content);
        } else {
            socket.emit(type, content);
        }
    }

    public boolean isRunning() {
        boolean retval = false;
        if (socket != null && socket.connected()) {
            retval = true;
        }
        return retval;
    }

    @Override
    public void call(Object... args) {
        JSONObject msg = (JSONObject) args[0];
        for (IOListener listener : listeners) {
            if (listener != null) {
                listener.onIOMessage(msg);
            }
        }
    }
}
