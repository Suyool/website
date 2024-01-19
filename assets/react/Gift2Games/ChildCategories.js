import React, { useEffect } from "react";

const ChildCategories = ({ childCategories, handleChildCategoryClick, setBackLink }) => {
    useEffect(() => {
        setBackLink("");
    }, []);

    return (
        <div id="Default_g2g">
            <div className="MainTitle">Select a Child Category</div>

            <div className="categories">
                {childCategories.map(({ id, title, image, hasChild }) => (
                    <div className="Cards" key={id} onClick={() => handleChildCategoryClick(id, hasChild)}>
                        <img className="logoImg" src={image} alt="gift2gamesLogo" />
                        <div className="Text">
                            <div className="SubTitle">{title}</div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ChildCategories;
