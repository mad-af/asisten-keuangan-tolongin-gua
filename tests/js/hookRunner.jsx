import React from "react";
import TestRenderer, { act } from "react-test-renderer";

export function renderHook(hook, { initialProps } = {}) {
    let result;
    function HookComp(props) {
        result = hook(props);
        return null;
    }
    let testRenderer;
    act(() => {
        testRenderer = TestRenderer.create(
            React.createElement(HookComp, initialProps)
        );
    });
    return {
        get result() {
            return { current: result };
        },
        update(props) {
            act(() => {
                testRenderer.update(React.createElement(HookComp, props));
            });
        },
        unmount() {
            act(() => {
                testRenderer.unmount();
            });
        },
    };
}

export { act } from "react-test-renderer";
