import React, { useEffect } from "react";

const Default = ({
                     SetVoucherData,
                     setActiveButton,
                     setHeaderTitle,
                     setBackLink,
                     categories,
                     handleCategoryClick,
                 }) => {
    useEffect(() => {
        setHeaderTitle("Gift2Games");
        setBackLink("default");
    }, []);

    return (
        <div id="Default_g2g">
            <div className="MainTitle">What do you want to do?</div>

            <div className="categories">
                {categories.map(({ id, title, image, hasChild }) => (
                    <div
                        className="Cards"
                        key={id}
                        onClick={() => handleCategoryClick(id, hasChild)}
                    >
                        <img
                            className="logoImg"
                            src={image}
                            alt="gift2gamesLogo"
                        />
                        <div className="Text">
                            <div className="SubTitle">{title}</div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Default;
