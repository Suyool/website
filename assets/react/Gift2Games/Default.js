import React, { useEffect } from "react";

const Default = ({
                     setHeaderTitle,
                     setBackLink,
                     categories,
                     handleCategoryClick,
                     desiredChildIdsMap, // Pass the desiredChildIdsMap as a prop
                 }) => {
    useEffect(() => {
        setHeaderTitle("Gift2Games");
        setBackLink("default");
    }, []);

    const categoryIds = Object.keys(desiredChildIdsMap);

    return (
        <div id="Default_g2g">
            <div className="MainTitle">What do you want to do?</div>

            <div className="categories">
                {categories
                    .filter((category) => categoryIds.includes(category.id))
                    .map(({ id, title, image, hasChild }) => (
                        <div
                            className="Cards"
                            key={id}
                            onClick={() => handleCategoryClick(id, hasChild)}
                        >
                            <img className="logoImg" src={image} alt="gift2gamesLogo" style={{ borderRadius: "50%" }} />
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
