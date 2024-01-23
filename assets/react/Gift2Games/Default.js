/*
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
*/
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

    const desiredChildIds = ["642", "636", "641", "704", "644", "703", "462", "469", "455", "457", "428", "647", "417", "575", "646", "414", "581", "582", "413", "343","477","298","633","624","1123","730","514","504","645","562", "558", "567", "905", "664", "656", "617", "496"];

    const desiredChilds = categories.flatMap(category => category.childs)
        .filter(child => desiredChildIds.includes(child.id));

    return (
        <div id="Default_g2g">
            <div className="MainTitle">What do you want to do?</div>

            <div className="categories">
                {desiredChilds.map((child) => (
                    <div
                        className="Cards"
                        key={child.id}
                        onClick={() => handleCategoryClick(child.id, child.hasChild)}
                    >
                        <img
                            className="logoImg"
                            src={child.image}
                            alt="gift2gamesLogo"
                        />
                        <div className="Text">
                            <div className="SubTitle">{child.title}</div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Default;
