import { createContext, useContext, useEffect, useState } from "react";
import { v4 as uuidv4 } from "uuid";

const ChatContext = createContext();

export const ChatProvider = ({ children }) => {
    const [deviceId, setDeviceId] = useState(null);

    useEffect(() => {
        let idDevice = localStorage.getItem('device_id');

        if (!idDevice) {
            idDevice = uuidv4();
            localStorage.setItem('device_id', idDevice);
        }
        setDeviceId(idDevice);
    }, []);

    return (
        <ChatContext.Provider value={{
            deviceId,
        }}>
            {children}
        </ChatContext.Provider>
    );
};

export const useChat = () => useContext(ChatContext);
