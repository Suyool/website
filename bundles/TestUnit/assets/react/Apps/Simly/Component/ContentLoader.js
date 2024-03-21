import React from "react";
import ContentLoader from "react-content-loader";

const CustomContentLoader = () => {
    return (
        <div className="row ps-3" style={{width: "100%"}}>
            <ContentLoader
                speed={2}
                width="100%"
                height="90vh"
                backgroundColor="#f3f3f3"
                foregroundColor="#ecebeb"
            >
                <rect x="0" y="0" rx="3" ry="3" width="100%" height="80"/>
                <rect x="0" y="90" rx="3" ry="3" width="100%" height="80"/>
                <rect
                    x="0"
                    y="180"
                    rx="3"
                    ry="3"
                    width="100%"
                    height="80"
                />
                <rect
                    x="0"
                    y="270"
                    rx="3"
                    ry="3"
                    width="100%"
                    height="80"
                />
                <rect
                    x="0"
                    y="360"
                    rx="3"
                    ry="3"
                    width="100%"
                    height="80"
                />
                <rect
                    x="0"
                    y="450"
                    rx="3"
                    ry="3"
                    width="100%"
                    height="80"
                />
            </ContentLoader>
        </div>
    );
};

export default CustomContentLoader;
