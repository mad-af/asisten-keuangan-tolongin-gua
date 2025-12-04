import { useForm } from '@inertiajs/inertia-react';
import React, { useState } from 'react'
import Sidebar from '../../Components/WhatsApp/SideBar';
import ChatWindow from '../../Components/WhatsApp/ChatWindow';
import { ChatProvider } from '../../Contexts/ChatContexts';
import useFetch from '../../Hooks/useFetch';

const Index = () => {

    return (
        <ChatProvider>
            <div className="h-screen w-full bg-base-200 flex">
                <Sidebar />
                <ChatWindow />
            </div>
        </ChatProvider>
    )
}

export default Index