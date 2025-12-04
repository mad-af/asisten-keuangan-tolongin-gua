import { createContext, useContext, useState } from "react";

const ChatContext = createContext();

export const ChatProvider = ({ children }) => {
    const [deviceId, setDeviceId] = useState(null);

    useEffect(() => {
        const id = localStorage.getItem("device_id");
        setDeviceId(id);
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
