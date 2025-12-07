import Page from "../ChooseYourSetup/Page.jsx";

test("ChooseYourSetup page exposes layout function", () => {
    expect(typeof Page).toBe("function");
    expect(typeof Page.layout).toBe("function");
    const wrapped = Page.layout(<div>child</div>);
    expect(wrapped.props.children.props.children).toBe("child");
});
